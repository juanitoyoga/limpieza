// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * AuditoriaEventos
 *
 * Registro inmutable de eventos del sistema.
 * Los detalles permanecen en la base de datos;
 * aquí solo se almacena una prueba criptográfica.
 */
contract AuditoriaEventos {
    event EventoRegistrado(
        uint8 indexed tipoEvento,
        uint256 indexed referenciaId,
        bytes32 indexed dataHash,
        address usuario,
        uint256 timestamp
    );

    /**
     * tipoEvento:
     * 1 = Denuncia creada
     * 2 = Denuncia validada
     * 3 = Denuncia aprobada
     * 4 = Multa emitida
     * 5 = Pago registrado
     * 6 = Contrato firmado
     * 7 = Trabajo ejecutado
     * 8 = Inspección
     * 9 = Otro
     */
    function registrarEvento(
        uint8 tipoEvento,
        uint256 referenciaId,
        bytes32 dataHash
    ) external {
        require(tipoEvento > 0, "Tipo invalido");
        require(dataHash != bytes32(0), "Hash invalido");

        emit EventoRegistrado(
            tipoEvento,
            referenciaId,
            dataHash,
            msg.sender,
            block.timestamp
        );
    }
}
