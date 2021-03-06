module.exports = {
  options: {
    banner   : '/*! <%= package.name %> - v<%= package.version %> - ' +
    '<%= grunt.template.today("yyyy-mm-dd") %> */\n',
    mangle   : {
      except: ['jQuery', 'Backbone']
    },
    sourceMap: true,
    compress : {
      global_defs : {
        "DEBUG": false
      },
      dead_code   : true,
      drop_console: true
    },
    beautify : false
  },

  dist: {
    files: {
      'js/build/inline-notes.min.js': ['js/build/inline-notes.js']
    }
  }
};