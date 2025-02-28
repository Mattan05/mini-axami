import React, { useContext } from 'react';
import { Link } from 'react-router-dom';

function Header({ isAuth, userRole }) {
    console.log(userRole);
    console.log('userrole');
    return ( 
        <header>
            {isAuth ? (/* DETTA KAN GÖRAS SNYGGARE BEHÖVER INTE UPPREPA HTML SÅDÄR */
                userRole.includes('ROLE_WORKER') ? 
                <Link to="/workerHome">
                    <div style={{cursor:'pointer'}} className="header-container d-flex justify-content-center">
                        <img 
                            src="logo.png" 
                            alt="Miniaxami logga" 
                            className="img-fluid" // Gör bilden responsiv
                            style={{ maxWidth: '150px' }} // Maxbredd
                        />
                    </div>

                </Link> 
                : 
                <Link to="/home">
                    <div style={{cursor:'pointer'}} className="header-container d-flex justify-content-center">
                        <img 
                            src="logo.png" 
                            alt="Miniaxami logga" 
                            className="img-fluid" // Gör bilden responsiv
                            style={{ maxWidth: '150px' }} // Maxbredd
                        />
                    </div>
                </Link>
            ) : (
                <Link to="/">
                    <div style={{cursor:'pointer'}} className="header-container d-flex justify-content-center">
                        <img 
                            src="logo.png" 
                            alt="Miniaxami logga" 
                            className="img-fluid" // Gör bilden responsiv
                            style={{ maxWidth: '150px' }} // Maxbredd
                        />
                    </div>
                </Link>
            )}
            <hr />
        </header>
    );
}

export default Header;
