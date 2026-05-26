import hardhat from "hardhat";

async function main() {
  // Obtenemos ethers desde el objeto hardhat
  const { ethers } = hardhat;

  // Obtenemos el contrato
  const ContratoDistribucion = await ethers.getContractFactory("ContratoDistribucion");

  // Desplegamos contrato
  const contrato = await ContratoDistribucion.deploy();

  // Esperamos confirmación
  await contrato.deployed();

  console.log("ContratoDistribucion desplegado en:", contrato.address);
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
