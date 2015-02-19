require(['react', 'jquery'], function (React) {
    var app = app || {},
        apiRoot = '/api/v1/';

    (function () {
        'use strict';

        var PlatesApp = React.createClass({
            handlePlateSearch: function (plate) {
                $.ajax({
                    url: apiRoot + 'plate',
                    dataType: 'json',
                    type: 'POST',
                    data: plate ,
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
                this.refs.plate.getDOMNode().value = '';
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