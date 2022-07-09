module.exports = {
    plugins: [
        require('postcss-mixins'),
        require('autoprefixer'),
        require('postcss-import'),
        require('postcss-nested'),
        require('cssnano')({
            preset: 'default',
        }),
    ]
}