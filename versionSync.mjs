import { readFileSync, writeFileSync } from "fs";

try {
  const pack = JSON.parse(readFileSync("package.json"));
  const baseFile = readFileSync("rek-ai.php");
  const baseString = baseFile
    .toString()
    .replace(/^(.*)Version:.*$/m, `$1Version: ${pack.version}`);
  writeFileSync("rek-ai.php", baseString);
  const readmeFile = readFileSync("readme.txt");
  const readmeString = readmeFile
    .toString()
    .replace(/^Stable tag:.*$/m, `Stable tag: ${pack.version}`);
  writeFileSync("readme.txt", readmeString);
  const potFile = readFileSync("languages/rek-ai.pot");
  const potString = potFile
    .toString()
    .replace(
      /^"Project-Id-Version:.*$/m,
      `"Project-Id-Version: Rek.ai ${pack.version}\\n"`,
    );
  writeFileSync("languages/rek-ai.pot", potString);
} catch (error) {
  console.error(error);
}
