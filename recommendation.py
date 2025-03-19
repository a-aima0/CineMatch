from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Example input (weather, mood, company) keywords
input_keywords = ['sunny', 'happy', 'friends']

# Example movie keywords (from the TMDb API)
movie_keywords = [
    ['bright', 'cheerful', 'adventure'],
    ['dark', 'horror', 'stormy'],
    ['cozy', 'peaceful', 'romantic'],
    # Other movie keywords...
]

# Use TF-IDF vectorizer to convert words to numerical vectors
vectorizer = TfidfVectorizer()

# Combine input keywords and movie keywords
all_keywords = input_keywords + [item for sublist in movie_keywords for item in sublist]

# Convert to TF-IDF vectors
tfidf_matrix = vectorizer.fit_transform(all_keywords)

# Calculate cosine similarity between input keywords and movie keywords
cosine_similarities = cosine_similarity(tfidf_matrix[:len(input_keywords)], tfidf_matrix[len(input_keywords):])

# Select movies with the highest similarity to the input
most_similar_movie_indexes = cosine_similarities.argmax(axis=1)

# Select relevant movies
recommended_movies = [movie_keywords[i] for i in most_similar_movie_indexes]
