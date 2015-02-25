define(['react', 'jquery', 'jsx!Alert'], function (React, $, Alert) {
    'use strict';

    var PlateSearch = React.createClass({
        handlePlateSearch: function (plate) {
            this.setState({ response: 'Fetching...', type: 'info' }, function () {
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
                    data: plate,
                    success: function (data) {
                        this.setState({ response: data.response.message, type: data.response.status });
                    }.bind(this),
                    error: function (xhr, status, err) {
                        console.error('Error!');
                        console.error(xhr);
                        console.error(this.props.url, status, err.toString());
                        this.setState({ response: err.toString(), type: status });
                    }.bind(this)
                });
            });
        },
        getInitialState: function () {
            return { response: '', type: '' };
        },
        render: function () {
            return (
                <div>
                    <PlateSearchForm onPlateSubmit={this.handlePlateSearch} />
                    <PlateSearchResponse response={this.state.response} type={this.state.type} />
                </div>
            );
        }
    });

    var PlateSearchForm = React.createClass({
        getInitialState: function () {
            return {
                currentPlate: '',
                previousPlate: ''
            };
        },
        handleSubmit: function (e) {
            e.preventDefault();

            var plateNumber = this.refs.plate.getDOMNode().value.trim();

            if (!plateNumber || (this.props.previousPlate == plateNumber)) {
                return;
            }

            this.props.onPlateSubmit({ plate: plateNumber });
            this.setState({
                previousPlate: plateNumber
            });
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