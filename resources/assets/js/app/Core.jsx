define(['react', 'router', 'jsx!PlateSearch', 'jsx!PlateFooter'], function(React, Router, PlateSearch, PlateFooter) {
    'use strict';

    // var PureRenderMixin = React.addons.PureRenderMixin;
    // mixins: [PureRenderMixin]

    var DefaultRoute = Router.DefaultRoute;
    var Link = Router.Link;
    var Route = Router.Route;
    var RouteHandler = Router.RouteHandler;

    var PlateApp = React.createClass({
        render: function () {
            return (
                <div>
                    <header>
                        <h1>Rego Checker</h1>
                    </header>

                    <section className="content">
                        <RouteHandler />
                    </section>

                    <PlateFooter />
                </div>
            );
        }
    });

    var routes = (
        <Route handler={PlateApp}>
            <Route name="search" handler={PlateSearch} />
            <DefaultRoute handler={PlateSearch} />
        </Route>
    );

    Router.run(routes, Router.HistoryLocation, function (Handler) {
        React.render(<Handler />, document.getElementById('view'));
    });
});