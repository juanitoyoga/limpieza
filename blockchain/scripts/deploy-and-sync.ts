import hardhat from "hardhat";
import fs from "fs";
import path from "path";

async function updateEnvFile(envPath: string, updates: Record<string, string>) {
  let envContent = fs.existsSync(envPath)
    ? fs.readFileSync(envPath, "utf8")
    : "";

  for (const [key, value] of Object.entries(updates)) {
    const regex = new RegExp(`^${key}=.*$`, "m");

    if (envContent.match(regex)) {
      // reemplaza si existe
      envContent = envContent.replace(regex, `${key}=${value}`);
    } else {
      // agrega si no existe
      envContent += `\n${key}=${value}`;
    }
  }

  fs.writeFileSync(envPath, envContent.trim() + "\n");
}

async function main() {
  const { ethers } = hardhat;

  console.log("====================================");
  console.log("🚀 Deploy + Sync Laravel .env");
  console.log("====================================");

  // -------------------------
  // 1. RegistroDocumentos
  // -------------------------
  const RegistroDocumentos = await ethers.getContractFactory("RegistroDocumentos");
  const registro = await RegistroDocumentos.deploy();
  await registro.waitForDeployment();
  const registroAddress = await registro.getAddress();

  console.log("RegistroDocumentos:", registroAddress);

  // -------------------------
  // 2. AuditoriaEventos
  // -------------------------
  const AuditoriaEventos = await ethers.getContractFactory("AuditoriaEventos");
  const auditoria = await AuditoriaEventos.deploy();
  await auditoria.waitForDeployment();
  const auditoriaAddress = await auditoria.getAddress();

  console.log("AuditoriaEventos:", auditoriaAddress);

  // -------------------------
  // 3. ContratoDistribucion
  // -------------------------
  const ContratoDistribucion = await ethers.getContractFactory("ContratoDistribucion");
  const contrato = await ContratoDistribucion.deploy();
  await contrato.waitForDeployment();
  const distribucionAddress = await contrato.getAddress();

  console.log("ContratoDistribucion:", distribucionAddress);

  // -------------------------
  // 4. RUTA .ENV LARAVEL
  // -------------------------
  const laravelEnvPath = path.resolve(__dirname, "../../.env");

  console.log("\n📦 Actualizando Laravel .env en:");
  console.log(laravelEnvPath);

  // -------------------------
  // 5. UPDATE .ENV
  // -------------------------
  await updateEnvFile(laravelEnvPath, {
    REGISTRO_DOCUMENTOS_ADDRESS: registroAddress,
    AUDITORIA_EVENTOS_ADDRESS: auditoriaAddress,
    CONTRATO_DISTRIBUCION_ADDRESS: distribucionAddress,
  });

  console.log("\n====================================");
  console.log("✔ .env actualizado correctamente");
  console.log("====================================");
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
