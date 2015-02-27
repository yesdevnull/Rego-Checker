define(['react', 'jquery', 'jsx!Alert', 'jsx!PlateNotify'], function (React, $, Alert, PlateNotify) {
    'use strict';

    var previousPlate = '';
    var currentPlate = '';

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
                    data: {
                        state: 'wa',
                        plate: plate
                    }
                }).done(function (data) {
                    currentPlate = plate.plate;

                    that.setState({
                        response: data.response.message,
                        type: data.response.status
                    });
                }).fail(function (xhr, status, err) {
                    if (typeof xhr.responseJSON != "undefined") {
                        that.setState({
                            response: xhr.responseJSON.message,
                            type: xhr.responseJSON.type
                        });
                    } else {
                        that.setState({
                            response: 'Unknown error occured',
                            type: 'error'
                        });
                    }
                });
            });
        },
        getInitialState: function () {
            return {
                response: '',
                type: ''
            };
        },
        render: function () {
            if (currentPlate && (this.state.type == 'success')) {
                var notify = <PlateNotify plate={currentPlate} />
            }

            return (
                <div className="box">
                    <PlateSearchForm onPlateSubmit={this.handlePlateSearch} />
                    <PlateSearchResponse response={this.state.response} type={this.state.type} />
                    {notify}
                </div>
            );
        }
    });

    var PlateSearchForm = React.createClass({
        handleSubmit: function (e) {
            e.preventDefault();

            currentPlate = '';
            var plateNumber = this.refs.plate.getDOMNode().value.trim();

            if (!plateNumber || (previousPlate == plateNumber)) {
                return;
            }

            this.props.onPlateSubmit({ plate: plateNumber });
            previousPlate = plateNumber;
        },
        render: function () {
            return (
                <form className="plateForm" onSubmit={this.handleSubmit}>
                    <input type="text" placeholder="Plate #" ref="plate" defaultValue={currentPlate} />
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