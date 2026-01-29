import { readFileSync, writeFileSync } from "fs";

try {
  const pack = JSON.parse(readFileSync("package.json"));

  replaceInFile("rek-ai.php", /Version:.*$/m, `Version: ${pack.version}`);
  replaceInFile(
    "readme.txt",
    /^Stable tag:.*$/m,
    `Stable tag: ${pack.version}`,
  );
  replaceInFile(
    "consts.php",
    /'REKAI_PLUGIN_VERSION', '.*'/m,
    `'REKAI_PLUGIN_VERSION', '${pack.version}'`,
  );
  replaceInFile(
    "languages/rek-ai.pot",
    /^"Project-Id-Version:.*$/m,
    `"Project-Id-Version: Rek.ai ${pack.version}\\n"`,
  );
} catch (error) {
  console.error(error);
}

function replaceInFile(file, search, replacement) {
  const fileContent = readFileSync(file, "utf8");
  const updatedContent = fileContent.toString().replace(search, replacement);
  writeFileSync(file, updatedContent);
}
