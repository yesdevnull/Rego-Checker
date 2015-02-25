define(['react', 'router', 'jsx!PlateHome', 'jsx!PlateSearch', 'jsx!PlateNotify', 'jsx!PlateFooter'], function(React, Router, PlateHome, PlateSearch, PlateNotify, PlateFooter) {
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
                        <ul id="nav">
                            <li><Link to="/">Home</Link></li>
                            <li><Link to="search">Search</Link></li>
                            <li><Link to="notify">Notify</Link></li>
                        </ul>
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
            <Route name="notify" handler={PlateNotify} />
            <DefaultRoute handler={PlateHome} />
        </Route>
    );

    Router.run(routes, Router.HistoryLocation, function (Handler) {
        React.render(<Handler />, document.getElementById('view'));
    });
});