define(['react'], function (React) {
    'use strict';

    var PlateHome = React.createClass({
        render: function () {
            return (
                <p>Plate Home</p>
            );
        }
    });

    var PlateHomeContainer = React.createClass({
        shouldComponentUpdate: function () {
            return false;
        },
        render: function () {
            return (
                <PlateHome />
            );
        }
    });

    return PlateHomeContainer;
});