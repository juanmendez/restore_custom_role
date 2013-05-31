<?php
/*
Plugin Name: Forum Roles
Plugin URI: http://example.com
Description: Creates custom roles and capabilities for a fictional forum plugin. On version 0.2, user's roles are saved
upon deactivation, and put back in place if plugin is activated again.
Version: 0.2
Author: WROX
Author URI: http://wrox.com
*/

/* Custom forum roles and capabilities class. */
class BOJ_Forum_Roles {

    /* PHP4 Constructor method. */
    function BOJ_Forum_Roles() {

        /* Register plugin activation hook. */
        register_activation_hook( __FILE__, array( &$this, 'activation' ) );

        /* Register plugin deactivation hook. */
        register_deactivation_hook( __FILE__, array( &$this, 'deactivation' ) );
    }

    /* Plugin activation method. */
    function activation() {

        /* Get the default administrator role. */
        $role =& get_role( 'administrator' );

        /* Add forum capabilities to the administrator role. */
        if ( !empty( $role ) ) {
            $role->add_cap( 'publish_forum_topics' );
            $role->add_cap( 'edit_others_forum_topics' );
            $role->add_cap( 'delete_forum_topics' );
            $role->add_cap( 'read_forum_topics' );
        }

        /* Create the forum administrator role. */
        add_role(
            'forum_administrator',
            'Forum Administrator',
            array(
                'publish_forum_topics',
                'edit_others_forum_topics',
                'delete_forum_topics',
                'read_forum_topics'
            )
        );

        /* Create the forum moderator role. */
        add_role(
            'forum_moderator',
            'Forum Moderator',
            array(
                'publish_forum_topics',
                'edit_others_forum_topics',
                'read_forum_topics'
            )
        );

        /* Create the forum member role. */
        add_role(
            'forum_member',
            'Forum Member',
            array(
                'publish_forum_topics',
                'read_forum_topics'
            )
        );

        /* Create the forum suspended role. */
        add_role(
            'forum_suspended',
            'Forum Suspended',
            array( 'read_forum_topics' )
        );

        /**
         * Juan Mendez ( update back to previous role )
         */
        $users = get_users( array('meta_key'=>'old_forum_role') );

        foreach( $users as $user )
        {
            $user->remove_role( 'subscriber' );
            $user->add_role(  get_user_meta($user->ID, 'old_forum_role', true ) );
            delete_user_meta( $user->ID, 'old_forum_role' );
        }

    }

    /* Plugin deactivation method. */
    function deactivation() {

        /* Get the default administrator role. */
        $role =& get_role( 'administrator' );

        /* Remove forum capabilities to the administrator role. */
        if ( !empty( $role ) ) {
            $role->remove_cap( 'publish_forum_topics' );
            $role->remove_cap( 'edit_others_forum_topics' );
            $role->remove_cap( 'delete_forum_topics' );
            $role->remove_cap( 'read_forum_topics' );
        }

        /* Get the default administrator role. */
        $role =& get_role( 'forum_administrator' );

        /* Remove forum capabilities to the administrator role. */
        if ( !empty( $role ) ) {
            $role->remove_cap( 'publish_forum_topics' );
            $role->remove_cap( 'edit_others_forum_topics' );
            $role->remove_cap( 'delete_forum_topics' );
            $role->remove_cap( 'read_forum_topics' );
        }


        /* Get the default administrator role. */
        $role =& get_role( 'forum_moderator' );

        /* Remove forum capabilities to the administrator role. */
        if ( !empty( $role ) ) {
            $role->remove_cap( 'publish_forum_topics' );
            $role->remove_cap( 'edit_others_forum_topics' );
            $role->remove_cap( 'read_forum_topics' );
        }

        /* Get the default administrator role. */
        $role =& get_role( 'forum_member' );

        /* Remove forum capabilities to the administrator role. */
        if ( !empty( $role ) ) {
            $role->remove_cap( 'publish_forum_topics' );
            $role->remove_cap( 'read_forum_topics' );
        }


        /* Get the default administrator role. */
        $role =& get_role( 'forum_suspended' );

        /* Remove forum capabilities to the administrator role. */
        if ( !empty( $role ) ) {
            $role->remove_cap( 'read_forum_topics' );
        }

        /* Set up an array of roles to delete. */
        $roles_to_delete = array(
            'forum_administrator',
            'forum_moderator',
            'forum_member',
            'forum_suspended'
        );

        /* Loop through each role, deleting the role if necessary. */
        /**
         * Juan Mendez, find users under each role and set them as subscribers
         * upon deactivation, save previous role..
         */
        foreach ( $roles_to_delete as $role ) {

            /* Get the users of the role. */
            $users = get_users( array( 'role' => $role ) );

            /* Check if there are no users for the role. */
            if ( count( $users ) > 0 )
            {
                foreach( $users as $user )
                {
                    $user->remove_role( $role );
                    $user->add_role( 'subscriber' );
                    add_user_meta( $user->ID, 'old_forum_role', $role, true );
                }
            }

            /* Remove the role from the site. */
            remove_role( $role );
        }
    }
}

$forum_roles = new BOJ_Forum_Roles();
?>