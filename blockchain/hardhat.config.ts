import * as dotenv from "dotenv";
dotenv.config({ path: ".env.blockchain" });

import { HardhatUserConfig } from "hardhat/config";
import "@nomicfoundation/hardhat-toolbox";

const config: HardhatUserConfig = {
  solidity: {
    version: "0.8.28",
    settings: {
      optimizer: {
        enabled: true,
        runs: 200,
      },
    },
  },

  paths: {
    sources: "./contracts",
    tests: "./test",
    cache: "./cache",
    artifacts: "../resources/js/artifacts",
  },

  networks: {
    localhost: {
      url: "http://127.0.0.1:8545",
    },
  },
};

const rpc = process.env.SEPOLIA_RPC_URL;
const key = process.env.SEPOLIA_PRIVATE_KEY;

if (rpc && key) {
  config.networks = {
    ...config.networks,
    sepolia: {
      url: rpc,
      accounts: [key],
    },
  };
}

export default config;