const schools = require('./schools/schools.service.js');
const annualcrimestats = require('./annualcrimestats/annualcrimestats.service.js');
const map = require('./map/map.service.js');
// eslint-disable-next-line no-unused-vars
module.exports = function (app) {
  app.configure(schools);
  app.configure(annualcrimestats);
  app.configure(map);
};
