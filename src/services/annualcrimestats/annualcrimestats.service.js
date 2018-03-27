// Initializes the `annualcrimestats` service on path `/annualcrimestats`
const createService = require('feathers-sequelize');
const createModel = require('../../models/annualcrimestats.model');
const hooks = require('./annualcrimestats.hooks');

module.exports = function (app) {
  const Model = createModel(app);

  const options = {
    name: 'annualcrimestats',
    Model
  };

  // Initialize our service with any options it requires
  app.use('/annualcrimestats', createService(options));

  // Get our initialized service so that we can register hooks and filters
  const service = app.service('annualcrimestats');

  service.hooks(hooks);
};
