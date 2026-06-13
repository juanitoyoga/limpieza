// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * RegistroDocumentos
 *
 * Registra la huella criptográfica (hash) de un documento
 * para demostrar su existencia e integridad.
 */
contract RegistroDocumentos {
    event DocumentoRegistrado(
        bytes32 indexed documentHash,
        uint256 indexed documentId,
        address indexed registradoPor,
        uint256 timestamp
    );

    mapping(bytes32 => bool) private registrados;

    function registrarDocumento(
        uint256 documentId,
        bytes32 documentHash
    ) external {
        require(documentHash != bytes32(0), "Hash invalido");
        require(!registrados[documentHash], "Documento ya registrado");

        registrados[documentHash] = true;

        emit DocumentoRegistrado(
            documentHash,
            documentId,
            msg.sender,
            block.timestamp
        );
    }

    function existe(bytes32 documentHash) external view returns (bool) {
        return registrados[documentHash];
    }
}
