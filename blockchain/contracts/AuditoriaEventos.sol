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
     * tipoEvento: DENUNCIAS
     * 1 = Denuncia pendiente
     * 2 = Denuncia verificada
     * 3 = Denuncia aprobada
     * 4 = Denuncia rechazada
     * 5 = Denuncia expirada
     * tipoEvento: MULTAS
     * 11 = Multa emitida
     * 12 = Multa pagada
     * 13 = Multa anulada
     * 14 = Multa desactivada
     * tipoEvento: CONTRATOS
     * 21 = Contrato pendiente
     * 22 = Contrato verificado
     * 23 = Contrato aprobado
     * 24 = Contrato rechazado
     * tipoEvento: NOMINACIONES
     * 31 = Nominación pendiente
     * 32 = Nominación verificada
     * 33 = Nominación aprobada
     * 34 = Nominación rechazada

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
