define(['react', 'router', 'jquery', 'jsx!Alert'], function (React, Router, $, Alert) {
    'use strict';

    var Link = Router.Link;

    var PlateSearch = React.createClass({
        handlePlateSearch: function (plate) {
            this.setState({ response: 'Fetching...', type: 'info' }, function () {
                var that = this;

                $.ajax({
                    url: '/api/v1/plate',
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (xhr) {
                        var token = $('meta[name="csrf_token"]').attr('content');

                        if (token) {
                            return xhr.setRequestHeader('X-XSRF-TOKEN', token);
                        }
                    },
                    data: plate
                }).done(function (data) {
                    that.setState({
                        response: data.response.message,
                        type: data.response.status,
                        previousPlate: plate.plate
                    });
                }).fail(function (xhr, status, err) {
                    that.setState({
                        response: xhr.responseJSON.message,
                        type: xhr.responseJSON.type,
                        previousPlate: plate.plate
                    });
                });
            });
        },
        getInitialState: function () {
            return {
                response: '',
                type: '',
                previousPlate: ''
            };
        },
        render: function () {
            if (this.state.previousPlate && (this.state.type == 'success')) {
                var notifyLink = <Link to="notify" params={{ plate: this.state.previousPlate }}>Notify</Link>;
            }

            return (
                <div className="box">
                    <PlateSearchForm previousPlate={this.state.previousPlate} onPlateSubmit={this.handlePlateSearch} />
                    <PlateSearchResponse response={this.state.response} type={this.state.type} />
                    {notifyLink}
                </div>
            );
        }
    });

    var PlateSearchForm = React.createClass({
        handleSubmit: function (e) {
            e.preventDefault();

            var plateNumber = this.refs.plate.getDOMNode().value.trim();

            if (!plateNumber || (this.props.previousPlate == plateNumber)) {
                return;
            }

            this.props.onPlateSubmit({ plate: plateNumber });
            this.props.previousPlate = plateNumber
        },
        render: function () {
            return (
                <form className="plateForm" onSubmit={this.handleSubmit}>
                    <input type="text" placeholder="Plate #" ref="plate" />
                    <input type="submit" value="Search" />
                    <p className="lawsuits"><strong>Note:</strong> I can't guarantee this data is correct.</p>
                </form>
            );
        }
    });

    var PlateSearchResponse = React.createClass({
        render: function () {
            return (
                <Alert type={this.props.type} inputClasses={this.props.type}>{this.props.response}</Alert>
            );
        }
    });

    return React.createClass({
        render: function () {
            return (
                <PlateSearch />
            );
        }
    });
});