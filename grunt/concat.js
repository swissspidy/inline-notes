module.exports =  {
  options: {
    banner: '/*! <%= package.version %> */\n',
    separator: ';'
  },
  maps: {
    src: ['js/src/inline-notes.js'],
    dest: 'js/build/inline-notes.js'
  }
};