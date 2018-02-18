// Initializes the `map` service on path `/map`
const createService = require('feathers-sequelize');
const createModel = require('../../models/map.model');
const hooks = require('./map.hooks');

module.exports = function (app) {
  const Model = createModel(app);
  const paginate = app.get('paginate');

  const options = {
    name: 'map',
    Model
  };

  // Initialize our service with any options it requires
  app.use('/map', createService(options));

  // Get our initialized service so that we can register hooks and filters
  const service = app.service('map');

  service.hooks(hooks);
};
