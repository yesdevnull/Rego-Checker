define(['react', 'classnames'], function (React, classNames) {
    'use strict';

    return React.createClass({
        render: function () {
            var inputClasses = classNames('message', 'text-center', this.props.inputClasses);

            return (
                <div className={inputClasses}>{this.props.children}</div>
            );
        }
    });
});