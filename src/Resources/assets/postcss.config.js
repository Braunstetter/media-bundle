const path = require("path");
//
module.exports = {
    plugins: [
        require('autoprefixer'),
        require('postcss-mixins')({
            mixinsFiles: path.join(__dirname, '/node_modules/@braunstetter/rock/js/mixins', '**/*.js')
        }),
        require('postcss-simple-vars'),
        require('postcss-import'),
        require('postcss-nested'),
        require('cssnano')({
            preset: 'default',
        }),
    ]
}