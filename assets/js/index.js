const requireAll = require.context('./', true, /\.js$/);
requireAll.keys().forEach(requireAll);