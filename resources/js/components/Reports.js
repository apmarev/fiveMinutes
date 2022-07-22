import React from 'react';
import ReactDOM from 'react-dom';

function Reports() {
    return (
        <>
            Test Component
        </>
    )
}

export default Reports;

if (document.getElementById('app')) {
    ReactDOM.render(<Reports />, document.getElementById('app'));
}
