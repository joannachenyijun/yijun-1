

# Resources Consulted
# https://en.wikipedia.org/wiki/Betweenness_centrality
# "A Faster Algorithm for Betweenness Centrality" by Ulrik Brandes
# "On Variants of Shortest-Path Betweenness Centrality and their Generic Computation" by Ulrik Brandes

import networkx as nx
import numpy.linalg
import heapq
import sys

# Lookup tables for nodes and indices
node_to_idx   = {}
idx_to_node   = {}
queue_lookup  = {}

# Print the progress of an operation
def print_progress( status, total ):
	sys.stdout.write("\r" + str(status) + "/" + str(total))
	sys.stdout.flush()
	if status == total:
		sys.stdout.write("\n")

# Allow for translation between a node id and its array index
def init_lookup_tables( G ):
	global node_to_idx
	global idx_to_node
	i = 0
	for node in G.nodes_iter():
		node_to_idx[node] = i
		idx_to_node[i] = node
		i += 1

# Add an item to the queue if is not already there
def update_queue( queue, node, distances ):
	global queue_lookup
	item = ( distances[ node_to_idx[ node ] ], node )
	if queue_lookup.get( node, False) == True:
		for i in range( 0, len( queue ) ):
			if queue[i][1] == node:
				del queue[i]
				heapq.heapify( queue )
				break
	heapq.heappush( queue, item )
	queue_lookup[ node ] = True
		
# Return the value with the nearest distance
def get_min_distance_node( queue ):
	global queue_lookup
	item = heapq.heappop( queue )
	node = item[1]
	queue_lookup[ node ] = False
	return node
		
# Find the centrality value for each node in Graph G
# Based on Pseudo Code found in 
# "A Faster Algorithm for Betweenness Centrality"
# and
# "On Variants of Shortest-Path Betweenness Centrality and their Generic Computation"
def find_centralities( G ):
	Centralities = [ 0 for n in G.nodes_iter() ]
	status = 0
	total = G.number_of_nodes()
	
	# Loop through each node to compute centrality
	for s in G.nodes_iter():
		# Print status
		status += 1
		print_progress(status, total)
		
		# Translate the node to an index
		s_idx = node_to_idx[s]
		
		# Perform the setup
		Stack             = []
		Queue             = []
		global            queue_lookup
		queue_lookup      = {}
		heapq.heapify( Queue )
		paths             = [ [] for n in G.nodes_iter()  ]
		path_counts       = [ 0 for n in G.nodes_iter()   ]
		distances         = [ -1  for n in G.nodes_iter() ]
		pair_dependencies = [ 0 for n in G.nodes_iter()   ]

		# Set the default values for the current node
		path_counts[s_idx] = 1
		distances[s_idx] = 0
		update_queue( Queue, s, distances )
		
		# BFS distance shortest path calculations
		while( len(Queue) > 0 ):
			v = get_min_distance_node( Queue )
			v_idx = node_to_idx[v]
			Stack.append( v )
			for w in G.neighbors( v ):
				w_idx = node_to_idx[w]
				weight = G[w][v].get('weight', 1.0)
				
				# Check for a shortest path
				if  distances[w_idx] > (distances[v_idx] + weight) or distances[w_idx] < 0:
					distances[w_idx] = distances[v_idx] + weight
					update_queue( Queue, w, distances )
					path_counts[ w_idx ] = 0
					paths[ w_idx ] = []

				# Count the the number of shortest paths	
				if abs(distances[w_idx] - (distances[v_idx] + weight)) < 0.0000000001:
					path_counts[w_idx] = path_counts[w_idx] + path_counts[v_idx]
					paths[w_idx].append( v )

		# Centrality calculations
		while len(Stack) > 0:
			w = Stack.pop()
			w_idx = node_to_idx[w]
			for v in paths[w_idx]:
				v_idx = node_to_idx[v]
				pair_dependencies[v_idx] = \
				    pair_dependencies[v_idx] + \
					( float(path_counts[v_idx])/ float(path_counts[w_idx])) * \
					( 1 + pair_dependencies[w_idx] )
				if w_idx != s_idx:
					Centralities[w_idx] = Centralities[w_idx] + pair_dependencies[w_idx]

	return Centralities

# Order the nodes from most central to least central
def get_central_nodes( centralities ):
	# Compute the normalization factor
	min_centrality = min( centralities )
	max_centrality = max( centralities )
	normalization_factor = max_centrality - min_centrality
	if normalization_factor == 0:
		normalization_factor = 1
	
	# Normalize the centralities
	central_nodes = [ 
		( idx_to_node[x], 
		( float(( centralities[x] - min_centrality )) / float(normalization_factor) ) )
		for x in range( 0, len( centralities ) ) 
		]

	# Sort the nodes based on centrality
	central_nodes = sorted( central_nodes, key=lambda x: x[1], reverse=True )
	return central_nodes

# Find the central nodes of the graph
def betweenness_centrality( G ):
	init_lookup_tables( G )
	centralities = find_centralities( G )
	return get_central_nodes( centralities )

if __name__ == "__main__":
	G = nx.Graph()
	if len( sys.argv ) == 2:		
		# Read in the graph
		G = nx.read_weighted_edgelist(sys.argv[1], comments='#' )
	else:
		print("Usage: python3 {} [graphfile]".format(sys.argv[0]))
		exit()

	# Draw the graph for debugging purposes
	#nx.draw( G )
	#plt.show()

	# Get a list of the most central nodes
	central_nodes = betweenness_centrality( G )

	# Print out the central nodes
	for node in central_nodes:
		print( "{}\t{}".format( str( node[0] ),str( node[1] ) ) )

