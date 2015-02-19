require(['react', 'jquery'], function (React) {
    var app = app || {},
        apiRoot = '/api/v1/';

    (function () {
        'use strict';

        var Plates = React.createClass({
            handlePlateSearch: function (plate) {
                var token;

                this.setState({ response: 'Fetching...', type: 'info' }, function () {
                    $.ajax({
                        url: apiRoot + 'plate',
                        dataType: 'json',
                        type: 'POST',
                        beforeSend: function (xhr) {
                            token = $('meta[name="csrf_token_alt"]').attr('content');

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
                        <header id="header">
                            <h1>Rego Search</h1>

                            <PlateForm onPlateSubmit={this.handlePlateSearch} />
                            <PlateResponse response={this.state.response} type={this.state.type} />
                        </header>
                    </div>
                );
            }
        });

        var PlateResponse = React.createClass({
            processResponseType: function(type, message) {
                switch (type) {
                    case 'success' :
                        return message;
                    break;

                    case 'warning' :
                        return 'Warning!  ' + message;
                    break;

                    case 'error' :
                        return 'Error: ' + message;
                    break;

                    case 'info' :
                        return 'Info: ' + message;
                    break;
                }
            },
            render: function () {
                var niceResponse = this.processResponseType(this.props.type, this.props.response);

                return (
                    <p>{niceResponse}</p>
                );
            }
        });

        var PlateForm = React.createClass({
            handleSubmit: function (e) {
                e.preventDefault();

                var plateNumber = this.refs.plate.getDOMNode().value.trim();

                if (!plateNumber) {
                    return;
                }

                this.props.onPlateSubmit({ plate: plateNumber });
            },
            render: function () {
                return (
                    <form className="plateForm" onSubmit={this.handleSubmit}>
                        <input type="text" placeholder="Plate #" ref="plate" />
                        <input type="submit" value="Plate" />
                    </form>
                );
            }
        });

        function render () {
            React.render(
                <Plates />,
                document.getElementById('view')
            );
        }

        render();
    })();
});