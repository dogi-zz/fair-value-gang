
// nodemon -e pine merge.js

const fs = require('fs');
const path = require('path');

const getterDefinitions = {};

const performFile = (sourceFile) => {
    const resultLines = fs.readFileSync(sourceFile, 'utf-8').split('\n');
    for (let i = 0; i < resultLines.length; i++) {
        let line = resultLines[i];

        const importMatch = line.match(/^\/\/IMPORT\s+(.+)/);
        if (importMatch) {
            const file = importMatch[1];
            const fileLines = performFile(path.resolve(path.dirname(sourceFile), file));
            resultLines.splice(i, 1, `// ... ${file}`);
            resultLines.splice(i + 1, 0, ...fileLines);
            continue;
        }
        const getterMatch = line.match(/^(get\S+)\(\)\s*=>\s*(\[.*\])/);
        if (getterMatch) {
            getterDefinitions[getterMatch[1]] = getterMatch[2];
            continue;
        }
        const getterCallMatch = line.match(/^  (get\S+)\(\),/);
        if (getterCallMatch) {
            resultLines[i] = `  ${getterDefinitions[getterCallMatch[1]]} = ${getterCallMatch[1]}(),`
            continue;
        }
    }
    return resultLines;
}

fs.readdirSync('pine').forEach(file => {
    if (file.startsWith('_main.') && file.endsWith('.pine')) {
        const resultLines = performFile(path.resolve('pine', file));
        const resultFile = file.substring(0, file.length - '.pine'.length) + '.result';
        console.info({ file, resultLines, resultFile });
        fs.writeFileSync(path.resolve('out', resultFile), resultLines.join('\n'))
    }
})

