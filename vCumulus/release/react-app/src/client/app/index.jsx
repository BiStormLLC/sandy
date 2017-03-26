import React from 'react';
import {render} from 'react-dom';

class App extends React.Component {
  render () {
  	return <p>Hello World!</p>
  };
  // We need to manager our scripts in here in order for it to be managed inside of reactJS.
}

render("<App/>", document.getElementById('app'));