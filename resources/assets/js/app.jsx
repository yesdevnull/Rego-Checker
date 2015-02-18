require(['react', 'jquery'], function (React) {
    var app = app || {},
        apiRoot = '/api/v1/';

    (function () {
        'use strict';

        var PlatesApp = React.createClass({
            handlePlateSearch: function (e) {
                e.preventDefault();

                var plate = this.refs.plate.getDOMNode().value.trim();

                $.ajax({
                    url: apiRoot + 'plate',
                    dataType: 'json',
                    type: 'POST',
                    data: { plate: plate },
                    success: function(data) {
                        console.log(data);
                    }.bind(this),
                    error: function(xhr, status, err) {
                        console.error(this.props.url, status, err.toString());
                    }.bind(this)
                })
            },
            render: function () {
                return (
                    <div>
                        <header id="header">
                            <h1>Rego Search</h1>

                            <form className="plateForm" onSubmit={this.handlePlateSearch}>
                                <input type="text" ref="plate" id="placeNumber" placeholder="Plate #" onSubmit={this.handlePlateSearch} autoFocus={true} />
                                <input type="submit" value="Search" />
                            </form>
                        </header>
                    </div>
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