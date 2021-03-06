// Initializes the `schools` service on path `/schools`
const createService = require('feathers-sequelize');
const createModel = require('../../models/schools.model');
const hooks = require('./schools.hooks');

module.exports = function (app) {
  const Model = createModel(app);
  const paginate = app.get('paginate');

  const options = {
    name: 'schools',
    Model,
    paginate
  };

  // Initialize our service with any options it requires
  app.use('/schools', createService(options));

  // Get our initialized service so that we can register hooks and filters
  const service = app.service('schools');

  service.hooks(hooks);
};
