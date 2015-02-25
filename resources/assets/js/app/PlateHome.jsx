define(['react'], function (React) {
    'use strict';

    var PlateHome = React.createClass({
        render: function () {
            return (
                <p>Plate Home</p>
            );
        }
    });

    return React.createClass({
        render: function () {
            return (
                <PlateHome />
            );
        }
    });
});