define(function(require) {
    var React = require('react');

    function App() {
        this.AppView = React.createClass({
            getInitialState: function() {
                return { value: "Plate #" };
            },
            handleChange: function(event) {
                this.setState({ value: event.target.value });
            },
            render: function() {
                var value = this.state.value;
                return <input type="text" value={value} onChange={this.handleChange} />;
            }
        });

    }

    App.prototype.init = function() {
        React.render(<this.AppView/>, document.body);
    };

    return App;
});