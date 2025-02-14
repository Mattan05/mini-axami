import React from 'react';
import { Link } from 'react-router-dom';

function Header({isAuth}) {
    return ( 
        <header>
         {isAuth ? 
        
            <Link to="/home">
                <div className="d-flex justify-content-center">
                    <h1 className="text-dark">MINI AXAMI</h1>
                </div>
            </Link>
        
        :
        <>
            <Link to="/">
                <div className="d-flex justify-content-center">
                    <h1 className="text-dark">MINI AXAMI</h1>
                </div>
            </Link>
        </>
    }
    </header>
     );
}

export default Header;