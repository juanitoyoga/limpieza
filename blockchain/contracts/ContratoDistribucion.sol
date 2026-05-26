// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * @title ContratoDistribucion
 * @author LimpiaTuRincon
 *
 * Registro público e inmutable de contratos de distribución.
 * Guarda únicamente la huella criptográfica (hash) del documento
 * y una referencia legible (ej: número de contrato).
 */
contract ContratoDistribucion {

    /// Evento emitido al registrar un contrato
    event ContratoRegistrado(
        bytes32 indexed documentHash,
        string numeroContrato,
        address indexed registradoPor,
        uint256 timestamp
    );

    /// Evita registrar el mismo documento más de una vez
    mapping(bytes32 => bool) private registrados;

    /**
     * Registrar un contrato de distribución
     *
     * @param documentHash Hash SHA-256 del PDF del contrato
     * @param numeroContrato Número o código interno del contrato
     */
    function registrarContrato(
        bytes32 documentHash,
        string calldata numeroContrato
    ) external {
        require(documentHash != bytes32(0), "Hash invalido");
        require(!registrados[documentHash], "Contrato ya registrado");

        registrados[documentHash] = true;

        emit ContratoRegistrado(
            documentHash,
            numeroContrato,
            msg.sender,
            block.timestamp
        );
    }

    /**
     * Verificar si un contrato ya fue registrado
     *
     * @param documentHash Hash SHA-256 del documento
     */
    function contratoExiste(bytes32 documentHash)
        external
        view
        returns (bool)
    {
        return registrados[documentHash];
    }
}

