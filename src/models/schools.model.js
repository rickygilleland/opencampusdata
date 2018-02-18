// See http://docs.sequelizejs.com/en/latest/docs/models-definition/
// for more of what you can do here.
const Sequelize = require('sequelize');
const DataTypes = Sequelize.DataTypes;

module.exports = function (app) {
  const sequelizeClient = app.get('sequelizeClient');
  const schools = sequelizeClient.define('schools', {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      allowNull: false
    },
    name: {
	    type: DataTypes.STRING,
	    allowNull: false
    },
    city: {
	    type: DataTypes.STRING
    },
    state: {
	    type: DataTypes.STRING
    },
    zip: {
	    type: DataTypes.STRING
    },
    latitude: {
	    type: DataTypes.STRING
    },
    longitude: {
	    type: DataTypes.STRING
    }
  }, {
	  timestamps: false
  }, {
    hooks: {
      beforeCount(options) {
        options.raw = true;
      }
    }
  });

  // eslint-disable-next-line no-unused-vars
  schools.associate = function (models) {
    // Define associations here
    // See http://docs.sequelizejs.com/en/latest/docs/associations/
  };

  return schools;
};
