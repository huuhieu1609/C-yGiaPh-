const { parse } = require('@vue/compiler-sfc');
const fs = require('fs');
const p = 'src/components/DoiTac/CayGiaPha/index.vue';
const content = fs.readFileSync(p, 'utf8');
try {
  parse(content, { filename: p });
  console.log('OK');
} catch (e) {
  console.error(e);
  process.exit(1);
}
