require(['react', 'router', 'jquery'], function (React, Router, $) {
    var app = app || {},
        apiRoot = '/api/v1/';

    (function () {
        'use strict';

        // var PureRenderMixin = React.addons.PureRenderMixin;
        // mixins: [PureRenderMixin]

        var DefaultRoute = Router.DefaultRoute;
        var Link = Router.Link;
        var Route = Router.Route;
        var RouteHandler = Router.RouteHandler;

        var PlatesApp = React.createClass({
            render: function () {
                return (
                    <div>
                        <header>
                            <ul id="nav">
                                <li><Link to="home">Home</Link></li>
                                <li><Link to="search">Search</Link></li>
                                <li><Link to="notify">Notify</Link></li>
                            </ul>
                        </header>

                        <section className="content">
                            <RouteHandler />
                        </section>

                        <FooterContainer />
                    </div>
                );
            }
        });

        var PlateHomeContainer = React.createClass({
            shouldComponentUpdate: function () {
                return false;
            },
            render: function () {
                var pageTitle = 'Home';

                document.title = pageTitle;

                return (
                    <PlateHome />
                );
            }
        });

        var PlateHome = React.createClass({
            render: function () {
                return (
                    <div>
                        <h1>Home!</h1>
                    </div>
                );
            }
        });

        var PlateNotify = React.createClass({
            render: function () {
                return (
                    <div>
                        <h1>Notify</h1>
                    </div>
                )
            }
        });

        var PlateSearch = React.createClass({
            handlePlateSearch: function (plate) {
                this.setState({ response: 'Fetching...', type: 'info' }, function () {
                    $.ajax({
                        url: apiRoot + 'plate',
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
                        }.bind(this)
                    });
                });
            },
            getInitialState: function () {
                return { response: '', type: '' };
            },
            render: function () {
                var pageTitle = 'Search';

                document.title = pageTitle;

                return (
                    <div>
                        <PlateSearchForm onPlateSubmit={this.handlePlateSearch} />
                        <PlateSearchResponse response={this.state.response} type={this.state.type} />
                    </div>
                );
            }
        });

        var PlateSearchResponse = React.createClass({
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

        var PlateSearchForm = React.createClass({
            handleSubmit: function (e) {
                e.preventDefault();

                var plateNumber = this.refs.plate.getDOMNode().value.trim();

                if (!plateNumber || (app.previousPlate == plateNumber)) {
                    return;
                }

                this.props.onPlateSubmit({ plate: plateNumber });
                app.previousPlate = plateNumber;
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

        var FooterContainer = React.createClass({
            shouldComponentUpdate: function () {
                return false;
            },
            render: function () {
                return (
                    <footer>
                        <FooterBox />
                    </footer>
                );
            }
        });

        var FooterBox = React.createClass({
            render: function () {
                return (
                    <p>
                        Brought to you by <a href="https://www.yesdevnull.net">Dan Barrett</a> &middot; <a href="https://github.com/yesdevnull/Rego-Checker">View on GitHub</a>
                    </p>
                );
            }
        });

        var routes = (
            <Route name="home" path="/" handler={PlatesApp}>
                <Route name="search" handler={PlateSearch} />
                <Route name="notify" handler={PlateNotify} />
                <DefaultRoute handler={PlateHomeContainer} />
            </Route>
        );

        Router.run(routes, Router.HistoryLocation, function (Handler) {
            React.render(<Handler />, document.getElementById('view'));
        });
    })();
});