// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

contract MultaMunicipal {

    struct Multa {
        address infractor;
        uint256 monto;
        uint256 fecha;
        string motivo;
        bool pagada;
    }

    Multa[] public multas;

    function emitirMulta(
        address _infractor,
        uint256 _monto,
        string memory _motivo
    ) public {
        multas.push(
            Multa(_infractor, _monto, block.timestamp, _motivo, false)
        );
    }

    function pagarMulta(uint256 _id) public payable {
        require(!multas[_id].pagada, "Multa ya pagada");
        require(msg.value == multas[_id].monto, "Monto incorrecto");

        multas[_id].pagada = true;
    }
}
