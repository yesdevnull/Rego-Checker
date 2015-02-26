define(['react', 'router', 'jquery'], function (React, Router, $) {
    'use strict';

    var PlateNotify = React.createClass({
        render: function () {
            var plate = this.props.plate;

            return (
                <div>
                    <PlateNotifyForm plate={plate} />
                </div>
            );
        }
    });

    var PlateNotifyForm = React.createClass({
        handleSubmit: function (e) {
            e.preventDefault();


        },
        render: function () {
            return (
                <form onSubmit={this.handleSubmit}>
                    <input type="email" placeholder="Email Address" ref="email" />
                    <input type="submit" value="Notify" />
                </form>
            );
        }
    });

    return React.createClass({
        render: function () {
            return (
                <PlateNotify {...this.props} />
            );
        }
    });
});