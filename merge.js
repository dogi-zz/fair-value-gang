
// nodemon -e pine merge.js

const fs = require('fs');
const path = require('path');

const getterDefinitions = {};

const performFile = (sourceFile, settings) => {
    const resultLines = fs.readFileSync(sourceFile, 'utf-8').split('\n');
    for (let i = 0; i < resultLines.length; i++) {
        let line = resultLines[i];

        const defineMatch = line.match(/^\s*\/\/DEFINE:\s*(.+?)\s*:\s*(.+?)\s*$/);
        if (defineMatch) {
            settings[defineMatch[1]] = defineMatch[2];
            console.info({ definMatch: defineMatch });
        }

        let importFile = null;
        const importIfMatch = line.match(/^\s*\/\/IMPORT IF\((.+?)\)\s+(.+)/);
        if (importIfMatch) {
            if (settings[importIfMatch[1]] === 'true') {
                importFile = importIfMatch[2];
            } else {
                continue;

            }
        }
        const importMatch = line.match(/^\s*\/\/IMPORT\s+(.+)/);
        if (!importIfMatch && importMatch) {
            importFile = importMatch[1];
        }
        if (importFile) {
            const fileLines = performFile(path.resolve(path.dirname(sourceFile), importFile), settings);
            resultLines.splice(i, 1, `// ... ${importFile}`);
            resultLines.splice(i + 1, 0, ...fileLines);
        }
    }
    return resultLines;
}

fs.readdirSync('pine').forEach(file => {
    if (file !== '_main.fvg.pine') {
        return;
    }
    if (file.startsWith('_main.') && file.endsWith('.pine')) {
        const resultLines = performFile(path.resolve('pine', file), {});
        const resultFile = file.substring(0, file.length - '.pine'.length) + '.result';
        console.info({ file, resultLines, resultFile });
        fs.writeFileSync(path.resolve('out', resultFile), resultLines.join('\n'))
    }
})

