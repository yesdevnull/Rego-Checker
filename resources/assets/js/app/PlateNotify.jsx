define(['react'], function (React) {
    'use strict';

    var PlateNotify = React.createClass({
        render: function () {
            return (
                <p>
                    Notify!
                </p>
            );
        }
    });

    return React.createClass({
        render: function () {
            return (
                <PlateNotify />
            );
        }
    });
});