const fs = require('fs');
const path = require('path');

function searchDir(dir, query) {
  const files = fs.readdirSync(dir);
  for (const file of files) {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);
    if (stat.isDirectory()) {
      searchDir(fullPath, query);
    } else if (file.endsWith('.ts') || file.endsWith('.tsx')) {
      const content = fs.readFileSync(fullPath, 'utf8');
      if (content.includes(query)) {
        console.log(`Found "${query}" in: ${fullPath}`);
      }
    }
  }
}

try {
  console.log('Searching for "SẮP CÓ"...');
  searchDir('C:\\Users\\minht\\CayGiaPha-Mobile\\src', 'SẮP CÓ');
  console.log('Searching for "Sắp ra mắt"...');
  searchDir('C:\\Users\\minht\\CayGiaPha-Mobile\\src', 'Sắp ra mắt');
} catch (e) {
  console.error(e);
}
