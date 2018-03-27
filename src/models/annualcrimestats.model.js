// See http://docs.sequelizejs.com/en/latest/docs/models-definition/
// for more of what you can do here.
const Sequelize = require('sequelize');
const DataTypes = Sequelize.DataTypes;

module.exports = function (app) {
  const sequelizeClient = app.get('sequelizeClient');
  const schools = sequelizeClient.define('annualcrimestats', {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      allowNull: false
    },
    schoolsId: {
	    type: DataTypes.INTEGER,
	    allowNull: false
    },
    type: {
	    type: DataTypes.STRING
    },
    year: {
	    type: DataTypes.INTEGER
    },
    data: {
	    type: DataTypes.JSON
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
