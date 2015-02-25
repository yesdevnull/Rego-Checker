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

    var PlateNotifyContainer = React.createClass({
        render: function () {
            return (
                <PlateNotify />
            );
        }
    });

    return PlateNotifyContainer;
});