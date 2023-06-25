const nodeHtmlToImage = require('node-html-to-image')
const fs = require('fs');


console.time("dbsave");
nodeHtmlToImage({
  output: './image.png',
  html: fs.readFileSync('index.html', 'utf8'),
  content: { name: 'you' }
})
  .then(() => console.timeEnd('dbsave'))
var stream = wkhtmltopdf(fs.createReadStream('file.html'));