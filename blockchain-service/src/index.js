'use strict';

require('dotenv').config();
const express  = require('express');
const helmet   = require('helmet');
const morgan   = require('morgan');
const { ethers } = require('ethers');

// ─── ABI del contrato AuditoriaEventos ───────────────────────────────────────
const ABI = [
  {
    "anonymous": false,
    "inputs": [
      { "indexed": true,  "internalType": "uint8",   "name": "tipoEvento",   "type": "uint8"   },
      { "indexed": true,  "internalType": "uint256",  "name": "referenciaId", "type": "uint256"  },
      { "indexed": true,  "internalType": "bytes32",  "name": "dataHash",     "type": "bytes32"  },
      { "indexed": false, "internalType": "address",  "name": "usuario",      "type": "address"  },
      { "indexed": false, "internalType": "uint256",  "name": "timestamp",    "type": "uint256"  }
    ],
    "name": "EventoRegistrado",
    "type": "event"
  },
  {
    "inputs": [
      { "internalType": "uint8",   "name": "tipoEvento",   "type": "uint8"   },
      { "internalType": "uint256", "name": "referenciaId", "type": "uint256"  },
      { "internalType": "bytes32", "name": "dataHash",     "type": "bytes32"  }
    ],
    "name": "registrarEvento",
    "outputs": [],
    "stateMutability": "nonpayable",
    "type": "function"
  }
];

// ─── Tipos de evento (espejo del contrato) ────────────────────────────────────
const TIPO_EVENTO = {
  DENUNCIA_CREADA:    1,
  DENUNCIA_VALIDADA:  2,
  DENUNCIA_APROBADA:  3,
  MULTA_EMITIDA:      4,
  PAGO_REGISTRADO:    5,
  CONTRATO_FIRMADO:   6,
  TRABAJO_EJECUTADO:  7,
  INSPECCION:         8,
  OTRO:               9,
};

// ─── Bootstrap ────────────────────────────────────────────────────────────────
let provider, wallet, contract;

function inicializarEthers() {
  const rpcUrl       = process.env.SEPOLIA_RPC_URL;
  const privateKey   = process.env.SEPOLIA_PRIVATE_KEY;
  const contractAddr = process.env.SEPOLIA_CONTRACT_ADDR;

  if (!rpcUrl)       throw new Error('SEPOLIA_RPC_URL no configurado');
  if (!privateKey)   throw new Error('SEPOLIA_PRIVATE_KEY no configurado');
  if (!contractAddr) throw new Error('SEPOLIA_CONTRACT_ADDR no configurado');

  // ethers requiere el prefijo 0x en la clave privada
  const pk = privateKey.startsWith('0x') ? privateKey : `0x${privateKey}`;

  provider = new ethers.JsonRpcProvider(rpcUrl, process.env.SEPOLIA_CHAIN_ID
    ? Number(process.env.SEPOLIA_CHAIN_ID)
    : undefined);
  wallet   = new ethers.Wallet(pk, provider);
  contract = new ethers.Contract(contractAddr, ABI, wallet);

  console.log(`[blockchain] Wallet: ${wallet.address}`);
  console.log(`[blockchain] Contrato: ${contractAddr}`);

  if (process.env.SEPOLIA_FROM_ADDRESS &&
      process.env.SEPOLIA_FROM_ADDRESS.toLowerCase() !== wallet.address.toLowerCase()) {
    console.warn(
      `[blockchain] ⚠️  SEPOLIA_FROM_ADDRESS (${process.env.SEPOLIA_FROM_ADDRESS}) ` +
      `no coincide con la wallet derivada de la clave privada (${wallet.address}). ` +
      `Revisa tu .env.blockchain.`
    );
  }
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Convierte un hex string (sin o con 0x) a bytes32.
 * Si el string ya tiene 32 bytes (64 hex chars) lo normaliza.
 * Si viene como hash SHA-256 de 64 chars lo usa directamente.
 */
function toBytes32(hexString) {
  const clean = hexString.startsWith('0x') ? hexString.slice(2) : hexString;
  if (clean.length > 64) throw new Error('Hash demasiado largo para bytes32');
  return '0x' + clean.padStart(64, '0');
}

function validarTipoEvento(tipo) {
  return Number.isInteger(tipo) && tipo >= 1 && tipo <= 255;
}

// ─── Lógica de negocio ────────────────────────────────────────────────────────

/**
 * Publica un evento en el smart contract y devuelve el tx hash.
 * Usa replacement automático si hay un nonce pendiente (reintento).
 */
async function publicarEvento(tipoEvento, referenciaId, dataHashHex) {
  const dataHashBytes32 = toBytes32(dataHashHex);

  const tx = await contract.registrarEvento(
    tipoEvento,
    referenciaId,
    dataHashBytes32,
    {
      // Sepolia: gas manual para evitar subestimaciones
      gasLimit: 100_000,
      maxFeePerGas:         ethers.parseUnits('20', 'gwei'),
      maxPriorityFeePerGas: ethers.parseUnits('2',  'gwei'),
    }
  );

  // Esperar confirmación (1 bloque)
  const receipt = await tx.wait(1);

  return {
    txHash:      tx.hash,
    blockNumber: receipt.blockNumber,
    gasUsado:    receipt.gasUsed.toString(),
  };
}

/**
 * Consulta los eventos de una referencia en los logs de Ethereum.
 *
 * NOTA: el plan gratuito de Alchemy limita eth_getLogs a rangos de 10 bloques.
 *
 * - Si se pasa "bloqueExacto", se hace UNA sola consulta en ese bloque
 *   (caso ideal: Laravel guardó el blockNumber al momento de registrar).
 * - Si no, se paginan los últimos "maxBloquesAtras" bloques de 10 en 10
 *   (más lento, usar solo como fallback).
 */
async function consultarEventos(referenciaId, maxBloquesAtras = 500, bloqueExacto = null) {
  const TAMANO_LOTE = 10; // límite del free tier de Alchemy
  const filtro = contract.filters.EventoRegistrado(null, referenciaId);

  // Camino rápido: ya sabemos en qué bloque (o cerca de qué bloque) ocurrió
  if (bloqueExacto) {
    const bloqueActual = await provider.getBlockNumber();
    const desde = Math.max(0, bloqueExacto - 2);
    const hasta = Math.min(bloqueActual, bloqueExacto + 2);
    const logs  = await contract.queryFilter(filtro, desde, hasta);
    return mapearLogs(logs);
  }

  // Camino lento: paginar hacia atrás de a 10 bloques
  const bloqueActual = await provider.getBlockNumber();
  const bloqueLimite  = Math.max(0, bloqueActual - maxBloquesAtras);

  let resultados = [];
  let hasta = bloqueActual;

  while (hasta > bloqueLimite) {
    const desde = Math.max(bloqueLimite, hasta - TAMANO_LOTE + 1);

    try {
      const logs = await contract.queryFilter(filtro, desde, hasta);
      resultados = resultados.concat(logs);
    } catch (err) {
      console.error(`[consultarEventos] Error en rango ${desde}-${hasta}:`, err.message);
    }

    hasta = desde - 1;
  }

  return mapearLogs(resultados);
}

function mapearLogs(logs) {
  return logs.map(log => ({
    tipoEvento:   Number(log.args.tipoEvento),
    referenciaId: log.args.referenciaId.toString(),
    dataHash:     log.args.dataHash,
    usuario:      log.args.usuario,
    timestamp:    new Date(Number(log.args.timestamp) * 1000).toISOString(),
    txHash:       log.transactionHash,
    bloque:       log.blockNumber,
  }));
}

// ─── Express app ──────────────────────────────────────────────────────────────
const app = express();
app.use(helmet());
app.use(morgan('tiny'));
app.use(express.json());

// Middleware: validar API key interna (Laravel → Microservicio)
app.use((req, res, next) => {
  const key = req.headers['x-internal-key'];
  if (!process.env.INTERNAL_API_KEY || key === process.env.INTERNAL_API_KEY) {
    return next();
  }
  return res.status(401).json({ error: 'No autorizado' });
});

// ─── Rutas ────────────────────────────────────────────────────────────────────

/**
 * POST /registrar
 * Body: { tipoEvento: int, referenciaId: int, dataHash: string }
 * Registra un evento en el smart contract.
 */
app.post('/registrar', async (req, res) => {
  try {
    const { tipoEvento, referenciaId, dataHash } = req.body;

    // Validaciones
    if (!validarTipoEvento(tipoEvento)) {
      return res.status(400).json({ error: 'tipoEvento debe ser un entero entre 1 y 255' });
    }
    if (!Number.isInteger(referenciaId) || referenciaId <= 0) {
      return res.status(400).json({ error: 'referenciaId debe ser entero positivo' });
    }
    if (!dataHash || typeof dataHash !== 'string' || dataHash.replace('0x','').length !== 64) {
      return res.status(400).json({ error: 'dataHash debe ser SHA-256 hex de 64 caracteres' });
    }

    const resultado = await publicarEvento(tipoEvento, referenciaId, dataHash);

    return res.status(201).json({
      ok:          true,
      txHash:      resultado.txHash,
      blockNumber: resultado.blockNumber,
      gasUsado:    resultado.gasUsado,
      explorerUrl: `https://sepolia.etherscan.io/tx/${resultado.txHash}`,
    });

  } catch (err) {
    console.error('[POST /registrar]', err.message);
    return res.status(500).json({ error: err.message });
  }
});

/**
 * GET /eventos/:referenciaId?desde=BLOCK_NUMBER
 *
 * Consulta todos los eventos registrados para una denuncia.
 *
 * Recomendado: pasar ?desde=<blockNumber> con el bloque devuelto por
 * POST /registrar al momento de crear el evento. Así la búsqueda es
 * instantánea en vez de escanear miles de bloques hacia atrás.
 *
 * Si no se pasa "desde", se buscan los últimos 500 bloques (~ última hora
 * en Sepolia), paginando de a 10 por el límite del free tier de Alchemy.
 */
app.get('/eventos/:referenciaId', async (req, res) => {
  try {
    const referenciaId = parseInt(req.params.referenciaId, 10);
    if (isNaN(referenciaId) || referenciaId <= 0) {
      return res.status(400).json({ error: 'referenciaId inválido' });
    }

    const desde = req.query.desde ? parseInt(req.query.desde, 10) : null;
    const maxBloquesAtras = desde ? null : 500;

    const eventos = await consultarEventos(referenciaId, maxBloquesAtras, desde);
    return res.json({ ok: true, total: eventos.length, eventos });

  } catch (err) {
    console.error('[GET /eventos]', err.message);
    return res.status(500).json({ error: err.message });
  }
});

/**
 * GET /health
 * Health check para Docker/Laravel.
 */
app.get('/health', async (req, res) => {
  try {
    const bloque = await provider.getBlockNumber();
    return res.json({
      ok:        true,
      red:       'sepolia',
      bloque,
      wallet:    wallet.address,
      contrato:  process.env.CONTRACT_ADDRESS,
    });
  } catch (err) {
    return res.status(503).json({ ok: false, error: err.message });
  }
});

// ─── Arranque ─────────────────────────────────────────────────────────────────
const PORT = process.env.PORT || 3001;

inicializarEthers();

app.listen(PORT, () => {
  console.log(`[blockchain-service] Escuchando en puerto ${PORT}`);
});

module.exports = app; // para tests
