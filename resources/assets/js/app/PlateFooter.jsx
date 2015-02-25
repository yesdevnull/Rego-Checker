define(['react'], function (React) {
    'use strict';

    var PlateFooter = React.createClass({
        render: function () {
            return (
                <footer>
                    <p>
                        Brought to you by <a href="https://www.yesdevnull.net">Dan Barrett</a> &middot; <a href="https://github.com/yesdevnull/Rego-Checker">View on GitHub</a>
                    </p>
                </footer>
            );
        }
    });

    var PlateFooterContainer = React.createClass({
        shouldComponentUpdate: function () {
            return false;
        },
        render: function () {
            return (
                <PlateFooter />
            )
        }
    });

    return PlateFooterContainer;
});