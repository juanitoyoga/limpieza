import * as dotenv from "dotenv";
dotenv.config({ path: "../.env" });

import "@nomicfoundation/hardhat-toolbox";

export default {
  paths: {
    artifacts: "../resources/js/artifacts",
    sources: "./contracts",
    tests: "./test",
    cache: "./cache",
  },

  solidity: "0.8.28",

  networks: {
    localhost: {
      url: "http://127.0.0.1:8545",
    },
    sepolia: {
      url: process.env.SEPOLIA_RPC_URL,
      accounts: [process.env.SEPOLIA_PRIVATE_KEY],
    },
  },
};
