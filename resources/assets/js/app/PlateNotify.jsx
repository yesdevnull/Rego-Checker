define(['react', 'jquery', 'jsx!Alert'], function (React, $, Alert) {
    'use strict';

    var PlateNotify = React.createClass({
        render: function () {
            var plate = this.props.plate;

            return (
                <div>
                    <p>You can optionally be notified when the plate {plate} has 30 days left until it expires by entering your email address below.</p>
                    <PlateNotifyForm plate={plate} />
                </div>
            );
        }
    });

    var PlateNotifyForm = React.createClass({
        getInitialState: function () {
            return {
                response: '',
                type: ''
            }
        },
        handleSubmit: function (e) {
            e.preventDefault();

            var email = this.refs.email.getDOMNode().value.trim();
            var plate = this.refs.plate.getDOMNode().value.trim();

            this.setState({}, function() {
                var that = this;

                $.ajax({
                    url: '/api/v1/subscribe',
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (xhr) {
                        var token = $('meta[name="csrf_token"]').attr('content');

                        if (token) {
                            return xhr.setRequestHeader('X-XSRF-TOKEN', token);
                        }
                    },
                    data: {
                        email: email,
                        plate: plate
                    }
                }).done(function (data) {
                    console.log(data);
                    
                    that.setState({
                        response: data.message,
                        type: data.type
                    });
                }).fail(function (xhr) {
                    console.log(xhr);
                    that.setState({
                        response: xhr.responseJSON.message,
                        type: xhr.responseJSON.type
                    });
                });
            });
        },
        render: function () {
            if ((this.state.type != '') && (this.state.response != '')) {
                var response = <PlateNotifyResponse response={this.state.response} type={this.state.type} />
            }

            return (
                <div>
                    <form onSubmit={this.handleSubmit}>
                        <input type="email" placeholder="Email Address" ref="email" />
                        <input type="hidden" ref="plate" value={this.props.plate} />
                        <input type="submit" value="Notify" />
                    </form>
                    {response}
                </div>
            );
        }
    });

    var PlateNotifyResponse = React.createClass({
        render: function () {
            return (
                <Alert type={this.props.type} inputClasses={this.props.type}>{this.props.response}</Alert>
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