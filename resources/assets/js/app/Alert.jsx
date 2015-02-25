define(['react', 'classnames', 'jsx!Element/Strong'], function (React, classNames, Strong) {
    'use strict';

    return React.createClass({
        render: function () {
            var inputClasses = classNames('message', 'text-center', this.props.inputClasses);
            var type = this.props.type;

            switch (type) {
                case 'warning' :
                case 'error' :
                    var boldText = type.charAt(0).toUpperCase() + type.slice(1);
                break;
            }

            return (
                <div className={inputClasses}>
                    <Strong>{boldText}</Strong>{this.props.children}
                </div>
            );
        }
    });
});