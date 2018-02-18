const assert = require('assert');
const app = require('../../src/app');

describe('\'map\' service', () => {
  it('registered the service', () => {
    const service = app.service('map');

    assert.ok(service, 'Registered the service');
  });
});
