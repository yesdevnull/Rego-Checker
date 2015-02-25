define(['react', 'router'], function (React, Router) {
    'use strict';

    var PlateNotify = React.createClass({
        render: function () {
            // var plate = this.props.plate;

            return (
                <p>
                    Notify!
                </p>
            );
        }
    });

    return React.createClass({
        mixins: [Router.State],
        render: function () {
            return (
                <PlateNotify plate={this.getParams().plate} />
            );
        }
    });
});