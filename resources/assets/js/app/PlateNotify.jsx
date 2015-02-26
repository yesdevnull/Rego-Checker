define(['react', 'jquery'], function (React, $) {
    'use strict';

    var PlateNotify = React.createClass({
        render: function () {
            var plate = this.props.plate;

            return (
                <div>
                    <p>You can optionally be notified when the plate {plate} has 30 left until it expires by entering your email address below.</p>
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
                <PlateNotify plate={this.props.plate} />
            );
        }
    });
});