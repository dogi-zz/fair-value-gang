
// nodemon -e pine merge.js

const fs = require('fs');

const getterDefinitions = {};

const performFile = (file) => {
  const resultLines = fs.readFileSync(file, 'utf-8').split('\n');
  for (let i = 0; i < resultLines.length; i++) {
    let line = resultLines[i];

    const importMatch = line.match(/^\/\/IMPORT\s+(.+)/);
    if (importMatch) {
      const file = importMatch[1];
      const fileLines = performFile(file);
      resultLines.splice(i, 1, `// ... ${file}`);
      resultLines.splice(i + 1, 0, ...fileLines);
      continue;
    }
    const getterMatch = line.match(/^(get\S+)\(\)\s*=>\s*(\[.*\])/);
    if (getterMatch){
      getterDefinitions[getterMatch[1]] = getterMatch[2];
      continue;
    }
    const getterCallMatch = line.match(/^  (get\S+)\(\),/);
    if (getterCallMatch){
      resultLines[i] = `  ${getterDefinitions[getterCallMatch[1]]} = ${getterCallMatch[1]}(),`
      continue;
    }

    // const inlineTypeMatch = line.match(/^\/\/type\s+(\S+)\s*\((.*)\)/);
    // if (inlineTypeMatch) {
    //   const name = inlineTypeMatch[1];
    //   const paramString = inlineTypeMatch[2];
    //   const paramCall = paramString.split(',').map(t => t.trim()).map(t => t.split(/\s+/)[1]).join(', ');
    //   inlineTypes[name] = {paramString, paramCall}
    //   resultLines[i] = `// (inline type ${name})`
    //   console.info({inlineTypes});
    //   continue;
    // }

    // Object.entries(inlineTypes).forEach(([name, type])=>{
    //   if (line.includes(`{${name}}`)){
    //     line = line.replace(`{${name}}`, inlineTypes[name].paramCall);
    //     resultLines[i] = line;
    //   }
    //   if (line.includes(`[${name}]`)){
    //     line = line.replace(`[${name}]`, inlineTypes[name].paramCall);
    //     resultLines[i] = line;
    //   }
    // })
  }
  return resultLines;
}

const mainLines = performFile('main.pine');
fs.writeFileSync('_result_', mainLines.join('\n'))

