require(['react', 'jquery'], function (React) {
    var app = app || {},
        apiRoot = '/api/v1/';

    (function () {
        'use strict';

        var PlatesApp = React.createClass({
            handlePlateSearch: function (plate) {
                var token;

                $.ajax({
                    url: apiRoot + 'plate',
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function(xhr) {
                        token = $('meta[name="csrf_token"]').attr('content');

                        if (token) {
                            return xhr.setRequestHeader('X-XSRF-TOKEN', token);
                        }
                    },
                    data: plate,
                    success: function(data) {
                        console.log(data);
                    }.bind(this),
                    error: function(xhr, status, err) {
                        console.error(this.props.url, status, err.toString());
                    }.bind(this)
                });
            },
            render: function () {
                return (
                    <div>
                        <header id="header">
                            <h1>Rego Search</h1>

                            <PlateForm onPlateSubmit={this.handlePlateSearch} />
                        </header>
                    </div>
                );
            }
        });

        var PlateForm = React.createClass({
            handleSubmit: function(e) {
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
                <PlatesApp />,
                document.getElementById('view')
            );
        }

        render();
    })();
});