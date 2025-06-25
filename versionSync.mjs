import { readFileSync, writeFileSync } from "fs";

try {
  const pack = JSON.parse(readFileSync("package.json"));
  const baseFile = readFileSync("rekai.php");
  const baseString = baseFile
    .toString()
    .replace(/^(.*)Version:.*$/m, `$1Version: ${pack.version}`);
  writeFileSync("rekai.php", baseString);
  const readmeFile = readFileSync("readme.txt");
  const readmeString = readmeFile
    .toString()
    .replace(/^Stable tag:.*$/m, `Stable tag: ${pack.version}`);
  writeFileSync("readme.txt", readmeString);
} catch (error) {
  console.error(error);
}
