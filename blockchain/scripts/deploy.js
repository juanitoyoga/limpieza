import hardhat from "hardhat";

async function main() {
  const { ethers } = hardhat;

  console.log("====================================");
  console.log("🚀 Desplegando contratos...");
  console.log("====================================");

  // =========================
  // 1. RegistroDocumentos
  // =========================
  const RegistroDocumentos = await ethers.getContractFactory("RegistroDocumentos");
  const registro = await RegistroDocumentos.deploy();
  await registro.waitForDeployment();
  const registroAddress = await registro.getAddress();

  console.log("✅ RegistroDocumentos:", registroAddress);

  // =========================
  // 2. AuditoriaEventos
  // =========================
  const AuditoriaEventos = await ethers.getContractFactory("AuditoriaEventos");
  const auditoria = await AuditoriaEventos.deploy();
  await auditoria.waitForDeployment();
  const auditoriaAddress = await auditoria.getAddress();

  console.log("✅ AuditoriaEventos:", auditoriaAddress);

  // =========================
  // 3. ContratoDistribucion
  // (tu versión adaptada)
  // =========================
  const ContratoDistribucion = await ethers.getContractFactory("ContratoDistribucion");
  const contrato = await ContratoDistribucion.deploy();
  await contrato.waitForDeployment();
  const distribucionAddress = await contrato.getAddress();

  console.log("✅ ContratoDistribucion:", distribucionAddress);

  // =========================
  // OUTPUT PARA LARAVEL
  // =========================
  console.log("\n====================================");
  console.log("📦 Copia esto en tu .env de Laravel:");
  console.log("====================================\n");

  console.log(`REGISTRO_DOCUMENTOS_ADDRESS=${registroAddress}`);
  console.log(`AUDITORIA_EVENTOS_ADDRESS=${auditoriaAddress}`);
  console.log(`CONTRATO_DISTRIBUCION_ADDRESS=${distribucionAddress}`);

  console.log("\n====================================");
  console.log("✔ Despliegue completado");
  console.log("====================================");
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});